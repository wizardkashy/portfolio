`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 06/02/2024 10:05:40 PM
// Design Name: 
// Module Name: ALUController
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module ALUController(
    input [2:0] ALUOp,
    input [6:0] Funct7,
    input [2:0] Funct3,
    output reg [3:0] Operation 
    );
    always @(*)
    begin
        case (Funct7)
            7'b0000000:
            begin
                case ({Funct3, ALUOp})
                    5'b11110: Operation = 4'b0000; // AND
                    5'b11010: Operation = 4'b0001; // OR
                    5'b10010: Operation = 4'b1100; // NOR
                    5'b01010: Operation = 4'b0111; // SLT
                    5'b00010: Operation = 4'b0010; // ADD
                endcase
            end
            7'b0100000:
            begin
                if ({Funct3, ALUOp} == 5'b00010) Operation = 4'b0110;
                else Operation = 4'bXXXX;
            end
            default:
            begin
                case ({Funct3, ALUOp})
                    5'b11100: Operation = 4'b0000; // ANDI
                    5'b11000: Operation = 4'b0001; // ORI
                    5'b10000: Operation = 4'b1100; // NORI
                    5'b01000: Operation = 4'b0010; // SLTI
                    5'b00000: Operation = 4'b0010; // ADDI
                    5'b01001: Operation = 4'b0010; // LW
                    5'b01001: Operation = 4'b0010; // SW
                    default: Operation = 4'bXXXX;
                endcase
            end
        endcase
    end
endmodule
